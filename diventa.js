const express = require('express');
const app = express();
const bodyParser = require('body-parser');
const cors = require('cors');
const axios = require('axios');

const mysql = require('mysql');

app.use(bodyParser.urlencoded({
    extended: true
}));
app.use(bodyParser.json());

var con = mysql.createConnection({
    host: "localhost",
    user: "denis",
    password: "oM36303690!",
    database: "ca_jadranka"
});

con.connect(function(err) {
    if (err) throw err;
    console.log('Connected to DB');
});

process.env['NODE_TLS_REJECT_UNAUTHORIZED'] = 0;

let username = 'japustrimapi';
let password = 'Ow25H91';
const token = `${username}:${password}`;
const encodedToken = Buffer.from(token).toString('base64');

//ove URL-ove bi isto trebalo da dobivamo sa MAPE!! tablica parametri u bazi!!!
const session_url_unitSatus_TEST = 'http://jacentar.jadranka.hr:8888/ords/test/pus/properties/'; //PROPERTY CODE BUDI PREBACIO U PARAMETRE KOJE DOBIVAMO SA MAPE!!!
const session_url_Reservation_TEST = 'https://diventa.hr:61111/kreirajRezervaciju';

let session_url_unitSatus = 'https://diventa.hr:61111/sobe'; //PROPERTY CODE BUDI PREBACIO U PARAMETRE KOJE DOBIVAMO SA MAPE!!!
const session_url_Reservation = 'https://diventa.hr:61111/kreirajRezervaciju';
const session_url_ReservationCancelation = 'https://diventa.hr:61111/stornirajRezervaciju';

//Post tentative reservation
app.post('/api/diventa/v1/res', (req, res) => {

    let resData = req.body.data;
    let propertyCode = resData.propertyCode;
    let unitId = resData.unitId;
    let dateFrom = resData.dateFrom;
    let dateTo = resData.dateTo;
    let nGuests = resData.numGuests;
    propertyCode = 'DEMOA6ber';

    let paramsdata = {
        "propertyId": propertyCode,
        "unitId": unitId,
        "dateFrom": dateFrom,
        "dateTo": dateTo,
        "brojOsoba": nGuests
    };

    var config = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'api_version': '1.0'
        },
        data: paramsdata
    };

    console.log('Podaci za rezervaciju', paramsdata)

    axios(session_url_Reservation, config)
        .then(function(response) {
            
            let posalji = {'reservation_id': response.data.uidRezervacije}
            console.log(JSON.stringify(posalji));
            res.send(JSON.stringify(posalji));
        })
        .catch(function(error) {
            console.log(error.response.data);
            res.send(error.response.data)
        });

});

//Cancel tentative reservation
app.post('/api/diventa/v1/cancelRes', (req, res) => {
    let resData = req.body.data;
    let reservationId = resData.reservationId;
    propertyCode = 'DEMOA6ber';

    let paramsdata = {
        "propertyId": propertyCode,
        "uidRezervacije": reservationId
    };

    var config = {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'api_version': '1.0'
        },
        data: paramsdata
    };

    console.log('Podaci za rezervaciju', paramsdata)

    axios(session_url_ReservationCancelation, config)
        .then(function(response) {
            console.log('Cancel rez Diventa', JSON.stringify(response.data));
            res.send(JSON.stringify(response.data))
        })
        .catch(function(error) {
            console.log(error.response.data);
            res.send(error.response.data)
        });


});

//Retrieve unit status
app.post('/api/diventa/v1/unitStatus', (req, res) => {

    let dates = req.body.data;
    let dateFrom = dates.dateFrom;
    let dateTo = dates.dateTo;
    let unit_code = dates.unit_code;
    let pmsid = dates.propertyId;
    let propertyId = dates.propertyId;
    pmsid = 'DEMOA6ber';
    propertyId = 'DEMOA6ber';

    let paramsdata;

    if (pmsid == undefined) {
        console.log('PMS_ID not defined', new Date(), dateFrom, dateTo);
        return;
    }

    if (unit_code != undefined) {
        paramsdata = {
            "propertyId": propertyId,
            "dateFrom": dateFrom,
            "dateTo": dateTo,
            "unitId": unit_code
        };
    } else {
        paramsdata = {
            "propertyId": propertyId,
            "dateFrom": dateFrom,
            "dateTo": dateTo
        };
    }

    var config = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'api_version': '1.0'
        },
        strictSSL: false,
        data: paramsdata
    };

    console.log('PARAMS DATA', paramsdata)

    let dostupnostData = {
        units: []
     };

    let UNITSTATUS_URL = session_url_unitSatus;

    axios(UNITSTATUS_URL, config)
        .then(function(response) {
            console.log('DIVENTA response', new Date(), pmsid, dateFrom, dateTo)
            //console.log('DIVENTA response real', new Date(), response.data)
            dostupnostData.units = response.data.Data.map(soba => {return {unit_code: soba.unitId, unit_id: '', unit_type_code:'', status: soba.status === 'slobodna' ? 'A' : 'C'}})
            //res.send(JSON.stringify(response.data))
            res.send(dostupnostData)
        })
        .catch(function(error) {
            console.log(error);
            console.log(new Date(), UNITSTATUS_URL, pmsid, unit_code, dateFrom, dateTo)
        });

});

//zapisivanje unit_code u bazu
app.post('/api/diventa/v1/updateUnitId', (req, res) => {

    let dates = req.body.data;
    let dateFrom = dates.dateFrom;
    let dateTo = dates.dateTo;
    let unit_code = dates.unit_code;
    let kampID = dates.kampID;
    let pmsid = dates.pmsid;

    let paramsdata;

    let MISH_DATA;

    if (unit_code != undefined) {
        paramsdata = {
            "arrival_date": dateFrom,
            "departure_date": dateTo,
            "unit_code": unit_code
        };
    } else {
        paramsdata = {
            "arrival_date": dateFrom,
            "departure_date": dateTo
        };
    }

    var config = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'api_version': '1.0',
            'Authorization': 'Basic ' + encodedToken
        },
        data: paramsdata
    };

    let codesToUpdate = [];

    let UNITSTATUS_URL = session_url_unitSatus + pmsid;

    MISH_DATA = axios(UNITSTATUS_URL, config)
        .then(function(response) {
            res.sendStatus(200)
            return response.data;
        })
        .then(function (podaci) {
            
            for (const key in podaci.units) {
                codesToUpdate.push([kampID, podaci.units[key]['unit_code'], podaci.units[key]['unit_id']]);
                let sqlUpdate = 'UPDATE brojSJ set pmsUnitId = ? WHERE kampId = ? and brojMISH = ?';

                con.query(sqlUpdate, [podaci.units[key]['unit_id'], kampID, podaci.units[key]['unit_code']], function(err) {
                    if (err) throw err;
                });
            }
        })
        .catch(function(error) {
            console.log(error);
        });            

});

app.listen(8160, () =>
    console.log('Campsabout DIVENTA API Server running on port 8160')
);