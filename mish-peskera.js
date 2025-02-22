const express = require("express");
const app = express();
const bodyParser = require("body-parser");
const cors = require("cors");
const axios = require("axios");

const mysql = require("mysql");

app.use(
  bodyParser.urlencoded({
    extended: true,
  })
);
app.use(bodyParser.json());

//dozvoljavamo samo sa campsabout
const corsOptions = {
  origin: "https://www.campsabout.com",
  optionsSuccessStatus: 200,
};

//app.use(cors(corsOptions));
app.use(cors()); //TODO ovo je samo za test, treba vratiti

var con = mysql.createConnection({
  host: "localhost",
  user: "denis",
  password: "oM36303690!",
  database: "ca_peskera",
});

con.connect(function (err) {
  if (err) throw err;
  console.log("Connected to DB");
});

let username = "kppustrimapi";
let password = "vUK7rza";
const token = `${username}:${password}`;
const encodedToken = Buffer.from(token).toString("base64");

//ove URL-ove bi isto trebalo da dobivamo sa MAPE!! tablica parametri u bazi!!!

let session_url_unitSatus =
  "https://www.loginsustavi.hr/ords/mish/pus/properties/"; //PROPERTY CODE BUDI PREBACIO U PARAMETRE KOJE DOBIVAMO SA MAPE!!!
const session_url_Reservation =
  "https://www.loginsustavi.hr/ords/mish/pus/reservation";

//Post tentative reservation
app.post("/api/mish-peskera/v1/res", async (req, res) => {
  let resData = req.body.data;
  let propertyCode = resData.propertyCode;
  let unitId = resData.unitId;
  let dateFrom = resData.dateFrom;
  let dateTo = resData.dateTo;
  let nGuests = resData.numGuests;

  await new Promise((resolve, reject) => {
    const sql =
      "SELECT pmsId FROM brojSJ JOIN vrstaSJ ON brojSJ.vrstaSJ = vrstaSJ.uid WHERE pmsUnitId = ? AND brojSJ.deleted IS NOT TRUE AND vrstaSJ.deleted IS NOT TRUE;";
    const vars = [unitId];
    con.query(sql, vars, (err, result) => {
      if (err) throw err;
      if (result[0]?.pmsId) propertyCode = result[0].pmsId;
      resolve();
    });
  });

  let paramsdata = {
    property_code: propertyCode,
    unit_id: unitId,
    arrival_date: dateFrom,
    departure_date: dateTo,
    number_of_guests: nGuests,
  };

  var config = {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      api_version: "1.0",
      Authorization: "Basic " + encodedToken,
    },
    data: paramsdata,
  };

  axios(session_url_Reservation, config)
    .then(function (response) {
      console.log(JSON.stringify(response.data));
      res.send(JSON.stringify(response.data));
    })
    .catch(function (error) {
      console.log(error.response.data);
      res.send(error.response.data);
    });
});

//Cancel tentative reservation
app.post("/api/mish-peskera/v1/cancelRes", (req, res) => {
  let resData = req.body.data;
  let reservationId = resData.reservationId;

  var config = {
    headers: {
      api_version: "1.0",
      Authorization: "Basic " + encodedToken,
    },
  };

  let url_tmp = session_url_Reservation + "/" + reservationId;

  axios
    .delete(url_tmp, config)
    .then(function (response) {
      console.log(JSON.stringify(response.data));
      res.send(JSON.stringify(response.data));
    })
    .catch(function (error) {
      //console.log(error.response.data);
      res.send(error.response.data);
    });
});

//Retrieve unit status
app.post("/api/mish-peskera/v1/unitStatus", async (req, res) => {
  const data = req.body.data;
  let dateFrom = data.dateFrom;
  let dateTo = data.dateTo;
  let unit_code = data.unit_code;
  let pmsid = data.pitchOrMobile == "P" ? "110" : "120";

  let paramsdata;

  if (unit_code) {
    paramsdata = {
      arrival_date: dateFrom,
      departure_date: dateTo,
      unit_code: unit_code,
    };
  } else {
    paramsdata = {
      arrival_date: dateFrom,
      departure_date: dateTo,
    };
  }

  var config = {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      api_version: "1.0",
      Authorization: "Basic " + encodedToken,
    },
    data: paramsdata,
  };

  let UNITSTATUS_URL = session_url_unitSatus + pmsid;

  axios(UNITSTATUS_URL, config)
    .then(function (response) {
      console.log("MISH response", new Date(), pmsid, dateFrom, dateTo);
      res.send(JSON.stringify(response.data));
    })
    .catch(function (error) {
      console.log(error);
      console.log(
        new Date(),
        UNITSTATUS_URL,
        pmsid,
        unit_code,
        dateFrom,
        dateTo
      );
      res.sendStatus(400);
    });
});

//zapisivanje unit_code u bazu
app.post("/api/mish-peskera/v1/updateUnitId", async (req, res) => {
  let dates = req.body.data;
  let dateFrom = dates.dateFrom;
  let dateTo = dates.dateTo;
  let unit_code = dates.unit_code;
  let kampID = dates.kampID;
  let pmsid = dates.pmsid;

  let paramsdata;

  let MISH_DATA;

  if (unit_code) {
    paramsdata = {
      arrival_date: dateFrom,
      departure_date: dateTo,
      unit_code: unit_code,
    };
  } else {
    paramsdata = {
      arrival_date: dateFrom,
      departure_date: dateTo,
    };
  }

  var config = {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      api_version: "1.0",
      Authorization: "Basic " + encodedToken,
    },
    data: paramsdata,
  };

  let codesToUpdate = [];

  let UNITSTATUS_URL = session_url_unitSatus + pmsid;

  MISH_DATA = axios(UNITSTATUS_URL, config)
    .then(function (response) {
      res.sendStatus(200);
      return response.data;
    })
    .then(function (podaci) {
      for (const key in podaci.units) {
        codesToUpdate.push([
          kampID,
          podaci.units[key]["unit_code"],
          podaci.units[key]["unit_id"],
        ]);
        let sqlUpdate =
          "UPDATE brojSJ set pmsUnitId = ? WHERE kampId = ? and brojMISH = ?";

        con.query(
          sqlUpdate,
          [
            podaci.units[key]["unit_id"],
            kampID,
            podaci.units[key]["unit_code"],
          ],
          function (err) {
            if (err) throw err;
          }
        );
      }
    })
    .catch(function (error) {
      console.log(error);
    });
});

app.listen(8013, () =>
  console.log("Campsabout MISH API Server (peskera) running on port 8013")
);
