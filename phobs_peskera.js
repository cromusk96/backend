const express = require("express");
const app = express();
const bodyParser = require("body-parser");
const cors = require("cors");
const axios = require("axios");
const cron = require("node-cron");
const moment = require("moment");
const builder = require("xmlbuilder"); //pogledaj dynamic-xml-builder, mislim da je bolji za ono sta nam treba
const XMLObject = require("dynamic-xml-builder"); //ovo je opcija 2 (pogledaj gornji red)
const rateLimit = require("express-rate-limit"); //da sprijecimo abuse MOZDA jos dodati cors da prihvaca samo sa jadranka-losinkj.com?
const xss = require("xss-clean");
const { XMLParser, XMLBuilder, XMLValidator } = require("fast-xml-parser");

const optionsXMLParser = {
  attributeNamePrefix: "",
  ignoreAttributes: false,
  ignoreNameSpace: false,
};
const mysql = require("mysql");

var con = mysql.createConnection({
  host: "localhost",
  user: "denis",
  password: "oM36303690!",
  database: "ca_peskera",
});

let PHOBS_API_ENDPOINT =
  "https://secure.phobs.net/webservice/pconnect/service.php";
let auth_username = "peskera_int_map_xml_2025";
let auth_password = "mqHf51PL0kC4Q46V";
let auth_siteid = "uo_pesk_map_siteID_01";

let propertyIds = {};
let unitIds = {};
let typeOfUnit = {};
let pmsTypeByPhobs = {};

let kampID = 1;

const currentYear = new Date().getFullYear(); //ovo nam treba za cron job/cjenike
let dateToday;

//TODO IZMEDJU OSTALOGA
//varijable u poseban config file?
//mozda kreirati poseban js samo za mysql connection
//mozda odvojiti svaki API u poseban js
//mozda odvojiti prikupljanje parametara (propertyId itd... u poseban .js)
//mozda napraviti prikupljanje cijena u nekom intervalu o odvojeni .js
//napravi upit za dohvat svih cijena (u frontendu na loadu ucitavamo sve cijene, prices from...), evo upita: SELECT c.datum, v.oznakaMISH, c.rateFrom FROM cjenik c INNER JOIN vrstaSJ v ON c.unitId = v.oznakaPhobs ORDER by oznakaMISH, datum
//OBAVEZNO implementirati winston logger ili nešto slično
//TODO BITNO: dodati kampID parametar, razmisliti kako cemo to tocno rjesiti (za vise kampova iste grupacije)

//PRIMJER PODATAKA KOJE JE POTREBNO POST-ATI za kreirati bookabilan link
/*{"data": {
    "brojSJ": "PPM028",
    "datumod": "2023-05-12",
    "brojdana": "7",
    "brojosoba": "2",
    "jezik": "en"
}
}*/
//KRAJ PRIMJER PODATAKA

//spajanje na bazu
//mozda bi trebalo napraviti pool i gasiti i paliti connection kako nam treba?
con.connect(function (err) {
  if (err) throw err;
  console.log("Connected to DB");
});

const parser = new XMLParser(optionsXMLParser);

//post dio prihvacamo podatke sa mape
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

app.use(xss());

const limiter = rateLimit({
  windowMs: 60 * 15 * 1000, // 15 minutes
  max: 1200, // Limit each IP to 15 requests per `window` (here, per 15 minutes)
  standardHeaders: true, // Return rate limit info in the `RateLimit-*` headers
  legacyHeaders: false, // Disable the `X-RateLimit-*` headers
});

// Apply the rate limiting middleware to all requests
app.use(limiter);

//za cijene po UnitId i period
function createXML_PCAvailabilityCalendarRQ(
  _propertyId_,
  _unitId_,
  _periodFrom_,
  _periodTo_
) {
  const data = new XMLObject("PCAvailabilityCalendarRQ");

  data.Auth.Username = auth_username;
  data.Auth.Password = auth_password;
  data.Auth.SiteId = auth_siteid;
  //data.SessionID = '';
  data.PropertyId = _propertyId_;

  data.Period._Start = _periodFrom_;
  data.Period._End = _periodTo_;

  if (_unitId_ !== "") {
    data.UnitId = _unitId_;
  } else {
    data.ShowUnits = "true";
  }

  return data.toXML({
    declaration: true,
  });
}

//kreiramo xml_doc za metodu PCPropertyAvailabilityRQ
function createXML_PCPropertyAvailabilityRQ(
  _unitId_,
  _propertyId_,
  _datumod_,
  _brojdana_,
  _brojosoba_,
  _djecaGodine_,
  _jezik_,
  _rateID_,
  enforceRateId = false
) {
  //ovdje dodati provjeru iz CMS-a->period da li ima smisla, da li je kamp otvoren, broj dana itd
  //takodjer provjerity da li postoji propertyid i svi ostali parametri

  console.log(
    "Creating xml:",
    "_unitId_:",
    _unitId_,
    "_propertyId_:",
    _propertyId_,
    "_datumod_:",
    _datumod_,
    "_brojdana_:",
    _brojdana_,
    "_brojosoba_:",
    _brojosoba_,
    "_djecaGodine_:",
    _djecaGodine_,
    "_jezik_:",
    _jezik_,
    "_rateID_:",
    _rateID_,
    "enforceRateId:",
    enforceRateId
  );
  const data = new XMLObject("PCPropertyAvailabilityRQ");

  if (!_jezik_) {
    _jezik_ = "en";
  }

  let childAge = [];

  data._Lang = _jezik_;

  /*data._encoding = 'utf-8';*/
  data.Auth.Username = auth_username;
  data.Auth.Password = auth_password;
  data.Auth.SiteId = auth_siteid;
  //data.SessionID = '';
  data.PropertyId = _propertyId_;
  data.UnitFilter.Date = _datumod_;
  data.UnitFilter.Nights = _brojdana_;

  if (_rateID_ && enforceRateId) data.UnitFilter.RateId = _rateID_;

  if (_unitId_) {
    data.UnitFilter.UnitId = _unitId_;
  }

  data.UnitFilter.UnitItem.Item.Adults = _brojosoba_;

  if (Object.keys(_djecaGodine_).length != 0) {
    Object.keys(_djecaGodine_).forEach((key) => {
      childAge.push(_djecaGodine_[key]);
    });

    data.UnitFilter.UnitItem.Item.Children.ChildAge = childAge;
  }

  return data.toXML({
    declaration: true,
  });
}

app.post("/api/phobs_peskera/v1/book", (req, res) => {
  //dodati provjeru da li su varijable postavljene i ispravne, ako nisu vrati error, nemoj niti slati na phobs
  kampID = req.body.data.kampId;

  con.changeUser({ database: "ca_" + req.body.data.grupacija }, function (err) {
    if (err) throw err;
  });

  var sql =
    "SELECT v.tip, v.oznakaMISH, v.oznakaPhobs, IF(v.tip = 'M', k.phobsIdMobilke, k.phobsIdParcele) as propertyPhobs from vrstaSJ v INNER JOIN kampovi k on v.kampId = k.uid WHERE v.kampId = ? AND k.deleted IS NOT TRUE AND v.deleted IS NOT TRUE";
  con.query(sql, [kampID], function (err, result, fields) {
    if (err) throw err;
    Object.keys(result).forEach(function (key) {
      var row = result[key];
      unitIds[row.oznakaMISH] = row.oznakaPhobs;
      propertyIds[row.tip] = row.propertyPhobs;
      typeOfUnit[row.oznakaMISH] = row.tip;
      pmsTypeByPhobs[row.oznakaPhobs] = row.oznakaMISH;
    });
  });

  let data = req.body.data;
  let _vrstaMish, _unitIdPhobs, _propertyOznaka, _propertyIdPhobs;
  let _datumod_, _brojdana_, _brojosoba_, _jezik_, _djecaGodine_;
  _datumod_ = data.datumod;
  _brojdana_ = data.brojdana;
  _brojosoba_ = data.brojosoba;
  _jezik_ = data.jezik;
  _djecaGodine_ = data.djecaGodine;
  _rateID_ = data.rateID;

  //uzimamo brojSJ iz POST-a i pitamo u bazi koji je to tip, zatim izvucemo unitIdPhobs i propertyIdPhobs
  // SELECT v.oznakaMISH FROM vrstaSJ v INNER JOIN brojSJ b ON v.uid = b.vrstaSJ and b.brojMISH = ?
  var sql =
    "SELECT v.oznakaMish as vrstaMISH FROM vrstaSJ v INNER JOIN brojSJ b ON v.uid = b.vrstaSJ AND v.kampId = b.kampId WHERE b.brojMISH = ? AND b.kampId = ? AND b.deleted IS NOT TRUE AND v.deleted IS NOT TRUE"; //kasnije ISPRAVI ovo i gledaj po vrstaID u tablici vrsteSJ (iznad je spreman upit)!!!!
  con.query(sql, [data.brojSJ, kampID], function (err, result, fields) {
    //mozda nam i ne treba callback, PROVJERITI!!!!!!
    if (err) throw err;
    var row = result[0];
    _vrstaMish = row.vrstaMISH;
    _unitIdPhobs = unitIds[row.vrstaMISH];
    _propertyOznaka = typeOfUnit[row.vrstaMISH];
    _propertyIdPhobs = propertyIds[typeOfUnit[row.vrstaMISH]];

    //kreiramo xml
    let xml_doc = createXML_PCPropertyAvailabilityRQ(
      _unitIdPhobs,
      _propertyIdPhobs,
      _datumod_,
      _brojdana_,
      _brojosoba_,
      _djecaGodine_,
      _jezik_,
      _rateID_,
      data.enforceRateId || false
    );

    //postamo na PHOBS API servis
    axios
      .post(PHOBS_API_ENDPOINT, xml_doc, {
        headers: {
          "Content-Type": "text/xml",
        },
      })
      .then((resp) => {
        let jObj = parser.parse(resp.data);
        let provjera = jObj;

        let price = Infinity;
        let url;
        const ratePlans = Array.isArray(
          jObj.PCPropertyAvailabilityRS.AvailabilityList.RatePlans.RatePlan
        )
          ? jObj.PCPropertyAvailabilityRS.AvailabilityList.RatePlans.RatePlan
          : [jObj.PCPropertyAvailabilityRS.AvailabilityList.RatePlans.RatePlan];

        ratePlans.forEach((ratePlan) => {
          if (!ratePlan?.Units?.Unit) return;
          const units = Array.isArray(ratePlan.Units.Unit)
            ? ratePlan.Units.Unit
            : [ratePlan.Units.Unit];

          units.forEach((unit) => {
            if (
              unit?.Rate?.StayTotal?.Price &&
              unit.Rate.StayTotal.Price < price
            ) {
              price = unit.Rate.StayTotal.Price;
              url = unit.BookUrl;
            }
          });
        });

        if (url) {
          res.send(JSON.stringify(url));
        } else {
          let error_messsage = {
            error_message:
              provjera?.PCPropertyAvailabilityRS?.ResponseType?.Errors?.Error[
                "#text"
              ],
            all_errors:
              provjera?.PCPropertyAvailabilityRS?.ResponseType?.Errors,
          };
          console.log(error_messsage);
          res.send(error_messsage);
          res.status(200).end();
        }
      })
      .catch((err) => {
        console.log(err);
        res.send(err);
      });
  });
});

app.post("/api/phobs_peskera/v1/totalPrices", async (req, res) => {
  const data = req.body.data;
  kampID = data.kampId;

  await new Promise((resolve, reject) => {
    con.changeUser({ database: "ca_" + data.grupacija }, function (err) {
      if (err) throw err;
      resolve();
    });
  });

  let oznakaPmsByOznakaPhobs = {};
  await new Promise((resolve, reject) => {
    const sql =
      "SELECT v.tip, v.oznakaMish, v.oznakaPhobs, v.phobsId as propertyPhobs from vrstaSJ v INNER JOIN kampovi k on v.kampId = k.uid WHERE coalesce(v.phobsId, '') <> '' AND v.kampId = ? AND v.deleted IS NOT TRUE AND k.deleted IS NOT TRUE";
    con.query(sql, [kampID], function (err, result, fields) {
      if (err) throw err;
      Object.keys(result).forEach(function (key) {
        var row = result[key];
        oznakaPmsByOznakaPhobs[row.oznakaPhobs] = row.oznakaMish;
      });
      resolve();
    });
  });

  let phobsPropertyIds = [];
  await new Promise((resolve, reject) => {
    const sql =
      "SELECT DISTINCT propertyPhobs AS phobsPropertyId FROM vrstaSJ WHERE kampId=? AND tip=? AND propertyPhobs IS NOT NULL AND deleted IS NOT TRUE;";
    const vars = [data.kampId, data.pitchOrMobile];
    con.query(sql, vars, (err, results) => {
      if (err) throw err;
      phobsPropertyIds = results.map((row) => row.phobsPropertyId);
      resolve();
    });
  });
  let defaultPhobsPropertyId = null;
  await new Promise((resolve, reject) => {
    const sql = `SELECT phobsId${
      data.pitchOrMobile?.toUpperCase() == "P" ? "Parcele" : "Mobilke"
    } AS defaultPhobsPropertyId FROM kampovi WHERE uid = ? AND deleted IS NOT TRUE;`;
    const vars = [data.kampId];
    con.query(sql, vars, (err, result) => {
      if (err) throw err;
      defaultPhobsPropertyId = result[0].defaultPhobsPropertyId;
      resolve();
    });
  });
  if (
    defaultPhobsPropertyId &&
    !phobsPropertyIds.includes(defaultPhobsPropertyId)
  )
    phobsPropertyIds.push(defaultPhobsPropertyId);

  let pricesTotal = {};
  let promises = [];

  phobsPropertyIds
    .map((phobsPropertyId) =>
      createXML_PCPropertyAvailabilityRQ(
        "",
        phobsPropertyId,
        data.datumod,
        data.brojdana,
        data.brojosoba,
        data.djecaGodine,
        data.jezik,
        null
      )
    )
    .forEach(async (xml_doc) => {
      promises.push(
        new Promise((resolve, reject) => {
          //postamo na PHOBS API servis
          axios
            .post(PHOBS_API_ENDPOINT, xml_doc, {
              headers: {
                "Content-Type": "text/xml",
              },
            })
            .then((response) => {
              let jObj = parser.parse(response.data);

              if (jObj?.PCPropertyAvailabilityRS?.ResponseType?.Errors) {
                console.log(jObj.PCPropertyAvailabilityRS.ResponseType.Errors);
                resolve();
                return;
              }

              if (
                !jObj?.PCPropertyAvailabilityRS?.AvailabilityList?.RatePlans
                  ?.RatePlan
              ) {
                resolve();
                return;
              }

              const ratePlans = Array.isArray(
                jObj.PCPropertyAvailabilityRS.AvailabilityList.RatePlans
                  .RatePlan
              )
                ? jObj.PCPropertyAvailabilityRS.AvailabilityList.RatePlans
                    .RatePlan
                : [
                    jObj.PCPropertyAvailabilityRS.AvailabilityList.RatePlans
                      .RatePlan,
                  ];

              ratePlans.forEach((ratePlan) => {
                if (!ratePlan?.Units?.Unit) return;
                const units = Array.isArray(ratePlan.Units.Unit)
                  ? ratePlan.Units.Unit
                  : [ratePlan.Units.Unit];

                units.forEach((unit) => {
                  const newEntry = {
                    price: unit?.Rate?.StayTotal?.Price,
                    url: unit?.BookUrl,
                    phobsUnitId: unit?.UnitId,
                  };
                  if (!newEntry.price || !newEntry.url || !newEntry.phobsUnitId)
                    return;
                  if (
                    !pricesTotal[
                      oznakaPmsByOznakaPhobs[newEntry.phobsUnitId]
                    ] ||
                    pricesTotal[oznakaPmsByOznakaPhobs[newEntry.phobsUnitId]]
                      .price > newEntry.price
                  )
                    pricesTotal[oznakaPmsByOznakaPhobs[newEntry.phobsUnitId]] =
                      newEntry;
                });
              });
              resolve();
            })
            .catch((err) => {
              console.log(err);
              resolve();
            });
        })
      );
    });
  await Promise.all(promises);
  res.send(pricesTotal);
});

app.post("/api/phobs_peskera/v1/getRates", async (req, res) => {
  const data = req.body.data;

  await new Promise((resolve, reject) => {
    con.changeUser({ database: "ca_" + data.group }, function (err) {
      if (err) throw err;
      resolve();
    });
  });

  let promises = [];
  let phobsPropertyId;
  promises.push(
    new Promise((resolve, reject) => {
      const sql =
        "SELECT propertyPhobs AS phobsPropertyId FROM vrstaSJ WHERE kampId=? AND oznakaPhobs=? AND propertyPhobs IS NOT NULL AND deleted IS NOT TRUE;";
      const vars = [data.propertyId, data.labelPhobs];
      con.query(sql, vars, (err, results) => {
        if (err) throw err;
        phobsPropertyId = results[0]?.phobsPropertyId;
        resolve();
      });
    })
  );
  let whitelist = {};
  promises.push(
    new Promise((resolve, reject) => {
      const sql =
        "SELECT rateId FROM rateIdWhitelist WHERE kampId=? AND include IS TRUE AND deleted IS NOT TRUE;";
      const vars = [data.propertyId];
      con.query(sql, vars, (err, results) => {
        if (err) throw err;
        results.forEach((r) => (whitelist[r.rateId] = true));
        resolve();
      });
    })
  );
  await Promise.all(promises);
  if (!phobsPropertyId) {
    await new Promise((resolve, reject) => {
      const sql =
        "SELECT IF(vrstaSJ.tip = 'M', kampovi.phobsIdMobilke, kampovi.phobsIdParcele) AS propertyPhobs FROM vrstaSJ JOIN kampovi ON vrstaSJ.kampId=kampovi.uid WHERE vrstaSJ.kampId = ? AND vrstaSJ.oznakaPhobs= ? AND vrstaSJ.deleted IS NOT TRUE;";
      const vars = [data.propertyId, data.labelPhobs];
      con.query(sql, vars, (err, result) => {
        if (err) throw err;
        phobsPropertyId = result[0]?.propertyPhobs;
        resolve();
      });
    });
  }
  if (!phobsPropertyId) {
    console.log("No phobsPropertyId");
    return res.sendStatus(400);
  }

  const xml_doc = createXML_PCPropertyAvailabilityRQ(
    data.labelPhobs,
    phobsPropertyId,
    data.dateFrom,
    data.duration,
    data.numberOfPeople,
    data.childrenAges,
    data.lang,
    null
  );

  let rates = [];
  //postamo na PHOBS API servis
  await axios
    .post(PHOBS_API_ENDPOINT, xml_doc, {
      headers: {
        "Content-Type": "text/xml",
      },
    })
    .then((response) => {
      let jObj = parser.parse(response.data);

      if (jObj?.PCPropertyAvailabilityRS?.ResponseType?.Errors) {
        console.log(jObj.PCPropertyAvailabilityRS.ResponseType.Errors);
        return res
          .status(response.status)
          .send(jObj.PCPropertyAvailabilityRS.ResponseType.Errors);
      }

      if (
        !jObj?.PCPropertyAvailabilityRS?.AvailabilityList?.RatePlans?.RatePlan
      ) {
        return res
          .status(400)
          .send({ error_message: "No Rate Plans received from PHOBS" });
      }

      const ratePlans = Array.isArray(
        jObj.PCPropertyAvailabilityRS.AvailabilityList.RatePlans.RatePlan
      )
        ? jObj.PCPropertyAvailabilityRS.AvailabilityList.RatePlans.RatePlan
        : [jObj.PCPropertyAvailabilityRS.AvailabilityList.RatePlans.RatePlan];

      ratePlans.forEach((ratePlan) => {
        if (!ratePlan?.Units?.Unit) return;
        const unit = Array.isArray(ratePlan.Units.Unit)
          ? ratePlan.Units.Unit[0]
          : ratePlan.Units.Unit;

        const newEntry = {
          name: ratePlan?.Name,
          description: ratePlan?.ShortDescription,
          price: unit?.Rate?.StayTotal?.Price,
          rateId: ratePlan?.RateId,
        };
        if (!newEntry.price || !newEntry.rateId) return;
        rates.push(newEntry);
      });
    });
  res.send(rates.filter((rate) => whitelist[rate.rateId]));
});

//cijene po danima
//example POST data
/*{
    "data": {
        "propertyId": "06566d3ea148c05e4f268f3fadfc00fd",
        "unitId": "",
        "dateFrom": "2023-07-09",
        "dateTo": "2023-07-21"
    }
}*/

app.post("/api/phobs_peskera/v1/prices", (req, res) => {
  let data = req.body.data;
  let _propertyId, _unitId, _dateFrom, _dateTo;
  _propertyId = data.propertyId;
  _unitId = data.unitId;
  _dateFrom = data.dateFrom;
  _dateTo = data.dateTo;

  kampID = req.body.data.kampId;

  var sql =
    "SELECT v.tip, v.oznakaMISH, v.oznakaPhobs, IF(v.tip = 'M', k.phobsIdMobilke, k.phobsIdParcele) as propertyPhobs from vrstaSJ v INNER JOIN kampovi k on v.kampId = k.uid WHERE v.kampId = ? AND v.deleted IS NOT TRUE AND k.deleted IS NOT TRUE";
  con.query(sql, [kampID], function (err, result, fields) {
    if (err) throw err;
    Object.keys(result).forEach(function (key) {
      var row = result[key];
      unitIds[row.oznakaMISH] = row.oznakaPhobs;
      propertyIds[row.tip] = row.propertyPhobs;
      typeOfUnit[row.oznakaMISH] = row.tip;
      pmsTypeByPhobs[row.oznakaPhobs] = row.oznakaMISH;
    });
  });

  let xml_doc = createXML_PCAvailabilityCalendarRQ(
    _propertyId,
    _unitId,
    _dateFrom,
    _dateTo
  );

  //postamo na PHOBS API servis
  axios
    .post(PHOBS_API_ENDPOINT, xml_doc, {
      headers: {
        "Content-Type": "text/xml",
      },
    })
    .then((resp) => {
      let jObj = parser.parse(resp.data);
      let provjera =
        jObj.PCAvailabilityCalendarRS.AvailabilityCalendar.Availability;

      const pricesToInsert = [];
      let _unitIdHelper, _rateFromHelper;

      //treba napraviti da azuriramo tablicu u bazi sa cijenama. razmisliti da li insert or update ili delete sve pa insert opet sve... UBACIO SAM U BAZU ALI TREBA UPDATE ILI OVO DRUGO...
      provjera.forEach(function (key, index) {
        _unitIdHelper = key.UnitId !== undefined ? key.UnitId : _unitId;
        _rateFromHelper =
          key.RateFromValue !== undefined ? key.RateFromValue : 0;

        pricesToInsert.push([kampID, key.Date, _unitIdHelper, _rateFromHelper]);
      });

      let sqlCijene =
        "INSERT INTO cjenik (kampId, datum, unitId, rateFrom) VALUES ? ON DUPLICATE KEY UPDATE rateFrom = VALUES(rateFrom)";

      con.query(sqlCijene, [pricesToInsert], function (err) {
        if (err) throw err;
        //con.end();
        //POPRAVI OVO, kao sta gore pise, treba conn start kada treba i onda conn end kada vise ne treba
      });

      res.send(JSON.stringify(jObj)); //ovo vjerovatno mozemo izbaciti jer samo spremamo u bazu, ne moramo nigdje vraćati, PROVJERI
      res.status(200).end();
    })
    .catch((err) => {
      console.log(err);
      //ovdje treba jos res.errtor message
    });
});

//jos jednom radimo istu proceduru, ovaj puta za cron job
async function runJob_PCAvailabilityCalendarRQ(propertyId) {
  dateToday = moment().format("YYYY-MM-DD");

  let _propertyId, _unitId, _dateFrom, _dateTo;
  _propertyId = propertyId;
  _unitId = "";
  _dateFrom = dateToday; //od dana ne mora biti cijela godina nego stavi da ide od danasnjeg dana
  _dateTo = currentYear + "-12-31";

  kampID = "1";

  con.changeUser({ database: "ca_polidor" }, function (err) {
    if (err) throw err;
  });

  var sql =
    "SELECT v.tip, v.oznakaMISH, v.oznakaPhobs, IF(v.tip = 'M', k.phobsIdMobilke, k.phobsIdParcele) as propertyPhobs from vrstaSJ v INNER JOIN kampovi k on v.kampId = k.uid WHERE v.kampId = ? AND v.deleted IS NOT TRUE AND k.deleted IS NOT TRUE";
  con.query(sql, [kampID], function (err, result, fields) {
    if (err) throw err;
    Object.keys(result).forEach(function (key) {
      var row = result[key];
      unitIds[row.oznakaMISH] = row.oznakaPhobs;
      propertyIds[row.tip] = row.propertyPhobs;
      typeOfUnit[row.oznakaMISH] = row.tip;
      pmsTypeByPhobs[row.oznakaPhobs] = row.oznakaMISH;
    });
  });

  let xml_doc = createXML_PCAvailabilityCalendarRQ(
    _propertyId,
    _unitId,
    _dateFrom,
    _dateTo
  );

  //postamo na PHOBS API servis
  axios
    .post(PHOBS_API_ENDPOINT, xml_doc, {
      headers: {
        "Content-Type": "text/xml",
      },
    })
    .then((resp) => {
      let jObj = parser.parse(resp.data);
      let provjera =
        jObj.PCAvailabilityCalendarRS.AvailabilityCalendar.Availability;

      const pricesToInsert = [];
      let _unitIdHelper, _rateFromHelper;

      provjera.forEach(function (key, index) {
        _unitIdHelper = key.UnitId !== undefined ? key.UnitId : _unitId;
        _rateFromHelper =
          key.RateFromValue !== undefined ? key.RateFromValue : 0;

        //mooozda dodati da snimi samo ako je rate <>0? tako nam ostaju stare cijene ako postoje, umjesto da ih pregazimo sa 0?
        //probati cemo maknuti nule
        if (_rateFromHelper != 0) {
          pricesToInsert.push([
            kampID,
            key.Date,
            _unitIdHelper,
            _rateFromHelper,
          ]);
        }
      });

      let sqlCijene =
        "INSERT INTO cjenik (kampId, datum, unitId, rateFrom) VALUES ? ON DUPLICATE KEY UPDATE rateFrom = VALUES(rateFrom)";

      con.query(sqlCijene, [pricesToInsert], function (err) {
        if (err) throw err;
        //con.end();
        //POPRAVI OVO, kao sta gore pise, treba conn start kada treba i onda conn end kada vise ne treba
      });
    })
    .catch((err) => {
      console.log(err);
      //ovdje treba jos res.errtor message
    });

  return;
}

async function callJobs() {
  //treba dodati log da znamo kada je napravljeno azuriranje i u slucaju da je bila greska
  await runJob_PCAvailabilityCalendarRQ(propertyIds.P);
  await runJob_PCAvailabilityCalendarRQ(propertyIds.M);
}

cron.schedule("0 0 */5 * * *", function () {
  console.log("---------------------");
  console.log("Ažuriram cjenik...");
  callJobs();
});

app.listen(8014, () =>
  console.log("Campsabout API Server running on port 8014")
);
