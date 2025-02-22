<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-type: application/json');

// Define a class for StoreSettings
class StoreSettings {
    public $useArrows;
    public $useDots;
    public $useThumbnail;
    public $groupName;
    public $propertyId;
    public $oldMapParameters;
    public $oldMapSettings;
    public $accommodationTypes; //['pitch', 'mobileHomeGlamping', 'apartment']
    public $specialConditions; //['plus1day', 'plus2days', 'plus3days', 'plus4days']
    public $promoCode;
    public $refreshAvailabilityPeriod;
    public $initialRecenter;
    public $alternativeMap;
    public $occupancyOptimization;
    public $show360onModal;
    public $mapboxStyle;
    public $mapboxAccessToken;
    public $extraSecurity;
    public $showUnavailableAcc;
    public $EUColoring;
    public $showAttribution;
    public $simplerFilter;

    public function __construct(
        $useArrows,
        $useDots,
        $useThumbnail,
        $groupName,
        $propertyId,
        $oldMapParameters,
        $oldMapSettings,
        $accommodationTypes,
        $specialConditions,
        $promoCode,
        $refreshAvailabilityPeriod,
        $initialRecenter,
        $mapPath,
        $occupancyOptimization,
        $show360onModal,
        $mapboxStyle,
        $mapboxAccessToken,
        $extraSecurity,
        $showUnavailableAcc,
        $EUColoring = true,
        $showAttribution = true,
        $simplerFilter = false

    ) {
        $this->useArrows = $useArrows;
        $this->useDots = $useDots;
        $this->useThumbnail = $useThumbnail;
        $this->groupName = $groupName;
        $this->propertyId = $propertyId;
        $this->oldMapParameters = $oldMapParameters;
        $this->oldMapSettings = $oldMapSettings;
        $this->accommodationTypes = $accommodationTypes;
        $this->specialConditions = $specialConditions;
        $this->promoCode = $promoCode;
        $this->refreshAvailabilityPeriod = $refreshAvailabilityPeriod;
        $this->initialRecenter = $initialRecenter;
        $this->mapPath = $mapPath ?? "{$groupName}_{$propertyId}";
        $this->occupancyOptimization = $occupancyOptimization;
        $this->show360onModal = $show360onModal;
        $this->mapboxStyle = $mapboxStyle ?? 'mapbox://styles/trimrd/clxork3wz00lx01qrhqc652s3';
        $this->mapboxAccessToken = $mapboxAccessToken ?? 'pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNrdDJqcGlzbDBsM2Eyd3Vscm40djZ2cmoifQ.q6UwItGIss7wbXA-6oD5OA';
        $this->extraSecurity = $extraSecurity ?? true;
        $this->showUnavailableAcc = $showUnavailableAcc ?? false;
        $this->EUColoring = $EUColoring;
        $this->showAttribution = $showAttribution;
        $this->simplerFilter = $simplerFilter;
    }
}

// Create store settings for each property
$cikatStoreSettings = new StoreSettings(
    true,                          // useArrows
    true,                         // useDots
    false,                          // useThumbnail
    'jadranka',                    // groupName
    1,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    ['pitch', 'mobileHomeGlamping'],       // accommodationTypes
    [],       // specialConditions
    false,                          // promoCode
    60000,                             // refreshAvailabilityPeriod
    [14.446841983449957,44.53673089767298],                             // initialRecenter
    "Cikat",                             // mapPath
    true,//occupancyOptimization 
    false,                           //show360onModal
    null,
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtMXRmZnE0aDAxdnMyaXNiMHRocmUxZ3oifQ.Ck8ntS5iLeg9yxN_H1Y9AA", 
    true, //extraSecurity
    true, //showUnavailableSJ
    false
);

$polidorStoreSettings = new StoreSettings(
    true,                         // useArrows
    true,                          // useDots
    false,                         // useThumbnail
    'polidor',                     // groupName
    1,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    ['pitch', 'mobileHomeGlamping'],                // accommodationTypes
    [], // specialConditions
    false,                         // promoCode
    45000,                             // refreshAvailabilityPeriod
    [13.5996718, 45.19148461],                             // initialRecenter
    'Polidor',                             // mapPath
    false,//occupancyOptimization 
    false,                           //show360onModal
    null,
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtMXRmZmZmOTAyNTQycHNidGxoaDg0YzEifQ.cxNzv8EA056vgpPSHpsDXg",
    true, //extraSecurity
    false //showUnavailableSJ
);

$oikosStoreSettings = new StoreSettings(
    true,                         // useArrows
    true,                          // useDots
    false,                         // useThumbnail
    'oikospag',                     // groupName
    1,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    ['mobileHomeGlamping'],                // accommodationTypes
    [], // specialConditions 
    true,                         // promoCode
    45000,                             // refreshAvailabilityPeriod
    [14.824590448815115,44.60163870756901],                             // initialRecenter
    "OI Pag",                             // mapPath
    false,//occupancyOptimization 
    false,                           //show360onModal
    null,
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtMXRmZXo2ejAxcmMya3NibGRocTEyZWkifQ.SvESHuEj8g7KjKRJEXjcBg",
    true, //extraSecurity
    false //showUnavailableSJ
);

$oikosBuqezStoreSettings = new StoreSettings(
    true,                         // useArrows
    true,                          // useDots
    false,                         // useThumbnail
    'oikospag',                     // groupName
    2,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    ['mobileHomeGlamping'],                // accommodationTypes
    [], // specialConditions 
    true,                         // promoCode
    45000,                             // refreshAvailabilityPeriod
    [15.554264722214157,43.87299649208009],                             // initialRecenter
    "OI BuqezVita",                             // mapPath
    false,//occupancyOptimization 
    false,                           //show360onModal
    null,
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtMXRmZXo2ejAxcmMya3NibGRocTEyZWkifQ.SvESHuEj8g7KjKRJEXjcBg",
    true, //extraSecurity
    false //showUnavailableSJ
);

$tihaStoreSettings = new StoreSettings(
    true,                         // useArrows
    true,                          // useDots
    false,                         // useThumbnail
    'tihasilo',                     // groupName
    1,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    ['pitch','mobileHomeGlamping'],                // accommodationTypes
    [], // specialConditions 
    false,                         // promoCode
    45000,                             // refreshAvailabilityPeriod
    [14.67234879819057,45.14880078455599],                             // initialRecenter
    'Tiha',                             // mapPath
    false,//occupancyOptimization 
    false,                           //show360onModal
    null,
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtMXRmZW1pZzAxeXcycHF6bXd4bzlrcjUifQ.Ywt8hs-8fYlHxRBsPLTpAw",
    true, //extraSecurity
    false //showUnavailableSJ
);

$ateaStoreSettings = new StoreSettings(
    true,                         // useArrows
    true,                          // useDots
    false,                         // useThumbnail
    'aminess',                     // groupName
    13,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    null,                // accommodationTypes
    [], // specialConditions 
    false,                         // promoCode
    45000,                             // refreshAvailabilityPeriod
    [14.54650806207996500,45.17041443880637500],                             // initialRecenter
    'Atea',                             // mapPath
    false,//occupancyOptimization 
    false,                           //show360onModal
    "mapbox://styles/trimrd/cm2szfr1n00di01pi87gcfw34",
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtMXRmZTl4djAxdzAyanF1YTd1d3J2Z20ifQ.xM3Mp7RzB4f5bYRvNSaWUw",
    false, //extraSecurity
    false, //showUnavailableSJ
    true, //EUColoring
    true, //showAttribution
    true //simplerFilter
);

$polariStoreSettings = new StoreSettings(
    true,                         // useArrows
    true,                          // useDots
    false,                         // useThumbnail
    'maistra',                     // groupName
    3,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    null,                // accommodationTypes
    [], // specialConditions 
    false,                         // promoCode
    45000,                             // refreshAvailabilityPeriod
    [13.674265863965445,45.05896661312954],                             // initialRecenter
    'Polari',                             // mapPath
    false,//occupancyOptimization 
    true,                           //show360onModal
    "mapbox://styles/trimrd/cm1tes6nt018p01qpd70i4w4t",
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtMXRmZHhocjAxZ3EyaXNiamEyYmxwNWwifQ.kI3ldRD57x_xxRvl3ytVSw",
    true, //extraSecurity
    false, //showUnavailableSJ
    true, //EUColoring
    false //showAttribution
);

$valkanelaStoreSettings = new StoreSettings(
    true,                         // useArrows
    true,                          // useDots
    false,                         // useThumbnail
    'maistra',                     // groupName
    4,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    null,                // accommodationTypes
    [], // specialConditions 
    false,                         // promoCode
    45000,                             // refreshAvailabilityPeriod
    [13.604784184129954,45.16553871784555],                             // initialRecenter
    'Valkanela',                             // mapPath
    false,//occupancyOptimization 
    true,                           //show360onModal
    "mapbox://styles/trimrd/cm1tes6nt018p01qpd70i4w4t",
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtMXRmZHhocjAxZ3EyaXNiamEyYmxwNWwifQ.kI3ldRD57x_xxRvl3ytVSw",
    true, //extraSecurity
    false, //showUnavailableSJ
    true, //EUColoring
    false //showAttribution
);

$vestarStoreSettings = new StoreSettings(
    true,                         // useArrows
    true,                          // useDots
    false,                         // useThumbnail
    'maistra',                     // groupName
    2,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    null,                // accommodationTypes
    [], // specialConditions 
    false,                         // promoCode
    45000,                             // refreshAvailabilityPeriod
    [13.686843236679067,45.052181700114744],                             // initialRecenter
    'Vestar',                             // mapPath
    false,//occupancyOptimization 
    true,                           //show360onModal
    "mapbox://styles/trimrd/cm1tes6nt018p01qpd70i4w4t",
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtMXRmZHhocjAxZ3EyaXNiamEyYmxwNWwifQ.kI3ldRD57x_xxRvl3ytVSw",
    true, //extraSecurity
    false, //showUnavailableSJ
    true, //EUColoring
    false //showAttribution
);

$amarinStoreSettings = new StoreSettings(
    true,                         // useArrows
    true,                          // useDots
    false,                         // useThumbnail
    'maistra',                     // groupName
    1,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    null,                // accommodationTypes
    [], // specialConditions 
    false,                         // promoCode
    45000,                             // refreshAvailabilityPeriod
    [13.620713121159554,45.105961536480635],                             // initialRecenter
    'Amarin',                             // mapPath
    false,//occupancyOptimization 
    true,                           //show360onModal
    "mapbox://styles/trimrd/cm1tes6nt018p01qpd70i4w4t",
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtMXRmZHhocjAxZ3EyaXNiamEyYmxwNWwifQ.kI3ldRD57x_xxRvl3ytVSw",
    true, //extraSecurity
    false, //showUnavailableSJ
    true, //EUColoring
    false //showAttribution
);

$portoSoleStoreSettings = new StoreSettings(
    true,                         // useArrows
    true,                          // useDots
    false,                         // useThumbnail
    'maistra',                     // groupName
    5,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    null,                // accommodationTypes
    [], // specialConditions 
    false,                         // promoCode
    45000,                             // refreshAvailabilityPeriod
    [13.6028559355519,45.14177056722497],                             // initialRecenter
    'Porto Sole',                             // mapPath
    false,//occupancyOptimization 
    true,                           //show360onModal
    "mapbox://styles/trimrd/cm1tes6nt018p01qpd70i4w4t",
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtMXRmZHhocjAxZ3EyaXNiamEyYmxwNWwifQ.kI3ldRD57x_xxRvl3ytVSw",
    true, //extraSecurity
    false, //showUnavailableSJ
    true, //EUColoring
    false //showAttribution
);

$zatonStoreSettings = new StoreSettings(
    true,                         // useArrows
    true,                          // useDots
    false,                         // useThumbnail
    'zaton',                     // groupName
    1,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    null,                // accommodationTypes
    [], // specialConditions 
    false,                         // promoCode
    45000,                             // refreshAvailabilityPeriod
    [15.168717053538359,44.233196775521975],                             // initialRecenter
    'Zaton',                             // mapPath
    false,//occupancyOptimization 
    false,                           //show360onModal
    null,
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtMmV1Y2Y3aDAyMXEyanM5YnltZHZmaXgifQ.b0WkbKQPFbCflcPJd0-hnA",
    true, //extraSecurity
    false //showUnavailableSJ
);

$slatinaStoreSettings = new StoreSettings(
    true,                         // useArrows
    true,                          // useDots
    false,                         // useThumbnail
    'jadranka',                     // groupName
    12,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    ['pitch', 'mobileHomeGlamping'],                // accommodationTypes
    [], // specialConditions 
    false,                         // promoCode
    60000,                             // refreshAvailabilityPeriod
    [14.341107340681049,44.82004796505609],                             // initialRecenter
    'Slatina',                             // mapPath
    true,//occupancyOptimization 
    false,                           //show360onModal
    null,
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtMXRmZnE0aDAxdnMyaXNiMHRocmUxZ3oifQ.Ck8ntS5iLeg9yxN_H1Y9AA",
    true, //extraSecurity
    true, //showUnavailableSJ
    false
);

$bijarStoreSettings = new StoreSettings(
    true,                         // useArrows
    true,                          // useDots
    false,                         // useThumbnail
    'jadranka',                     // groupName
    11,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    ['pitch', 'mobileHomeGlamping'],                // accommodationTypes
    [], // specialConditions 
    false,                         // promoCode
    60000,                             // refreshAvailabilityPeriod
    [14.39518548163656,44.69957868826677],                             // initialRecenter
    'Bijar',                             // mapPath
    true,//occupancyOptimization 
    false,                           //show360onModal
    null,
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtMXRmZnE0aDAxdnMyaXNiMHRocmUxZ3oifQ.Ck8ntS5iLeg9yxN_H1Y9AA",
    true, //extraSecurity
    true, //showUnavailableSJ
    false
);

$baldarinStoreSettings = new StoreSettings(
    true,                         // useArrows
    true,                          // useDots
    false,                         // useThumbnail
    'jadranka',                     // groupName
    10,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    ['pitch', 'mobileHomeGlamping'],                // accommodationTypes
    [], // specialConditions 
    false,                         // promoCode
    60000,                             // refreshAvailabilityPeriod
    [14.513596237576422,44.6124549067564],                             // initialRecenter
    'Baldarin',                             // mapPath
    true,//occupancyOptimization 
    false,                           //show360onModal
    null,
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtMXRmZnE0aDAxdnMyaXNiMHRocmUxZ3oifQ.Ck8ntS5iLeg9yxN_H1Y9AA",
    true, //extraSecurity
    true, //showUnavailableSJ
    false
);

$sirenaStoreSettings = new StoreSettings(
    true,                         // useArrows
    true,                          // useDots
    false,                         // useThumbnail
    'aminess',                     // groupName
    11,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    null,                // accommodationTypes
    [], // specialConditions 
    false,                         // promoCode
    45000,                             // refreshAvailabilityPeriod
    [13.575613448518453,45.31537903756009],                             // initialRecenter
    'Sirena',                             // mapPath
    false,//occupancyOptimization 
    false,                           //show360onModal
    "mapbox://styles/trimrd/cm2szfr1n00di01pi87gcfw34",
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtMXRmZTl4djAxdzAyanF1YTd1d3J2Z20ifQ.xM3Mp7RzB4f5bYRvNSaWUw",
    false, //extraSecurity
    false, //showUnavailableSJ
    true, //EUColoring
    true, //showAttribution
    true //simplerFilter
);

$maraveaStoreSettings = new StoreSettings(
    true,                         // useArrows
    true,                          // useDots
    false,                         // useThumbnail
    'aminess',                     // groupName
    12,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    null,                // accommodationTypes
    [], // specialConditions 
    false,                         // promoCode
    45000,                             // refreshAvailabilityPeriod
    [13.547027112852675,45.341962079173925],                             // initialRecenter
    'Maravea',                             // mapPath
    false,//occupancyOptimization 
    false,                           //show360onModal
    "mapbox://styles/trimrd/cm2szfr1n00di01pi87gcfw34",
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtMXRmZTl4djAxdzAyanF1YTd1d3J2Z20ifQ.xM3Mp7RzB4f5bYRvNSaWUw",
    false, //extraSecurity
    false, //showUnavailableSJ
    true, //EUColoring
    true, //showAttribution
    true //simplerFilter
);

$maslinicaStoreSettings = new StoreSettings(
    true,                         // useArrows
    true,                          // useDots
    false,                         // useThumbnail
    'maslinica',                     // groupName
    1,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    null,                // accommodationTypes
    [], // specialConditions 
    false,                         // promoCode
    45000,                             // refreshAvailabilityPeriod
    [14.148865166288289,45.07976956949375],                             // initialRecenter
    'MaslinicaOliva',                             // mapPath
    false,//occupancyOptimization 
    false,                           //show360onModal
    null,
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtM3B6bjRpZDBnMmsybHNmdWYzaTNqMjEifQ.bPzbb7YYthid1xd5MBNLew",
    true, //extraSecurity
    false //showUnavailableSJ
);

$olivaStoreSettings = new StoreSettings(
    true,                         // useArrows
    true,                          // useDots
    false,                         // useThumbnail
    'maslinica',                     // groupName
    1,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    null,                // accommodationTypes
    [], // specialConditions 
    false,                         // promoCode
    45000,                             // refreshAvailabilityPeriod
    [14.148865166288289,45.07976956949375],                             // initialRecenter
    'MaslinicaOliva',                             // mapPath
    false,//occupancyOptimization 
    false,                           //show360onModal
    null,
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtM3B6bjRpZDBnMmsybHNmdWYzaTNqMjEifQ.bPzbb7YYthid1xd5MBNLew",
    true, //extraSecurity
    false //showUnavailableSJ
);

$koversadaStoreSettings = new StoreSettings(
    true,                         // useArrows
    true,                          // useDots
    false,                         // useThumbnail
    'maistra',                     // groupName
    6,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    null,                // accommodationTypes
    [], // specialConditions 
    false,                         // promoCode
    45000,                             // refreshAvailabilityPeriod
    [13.61448,45.13385],                             // initialRecenter
    'Koversada',                             // mapPath
    false,//occupancyOptimization 
    true,                           //show360onModal
    "mapbox://styles/trimrd/cm1tes6nt018p01qpd70i4w4t",
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtMXRmZHhocjAxZ3EyaXNiamEyYmxwNWwifQ.kI3ldRD57x_xxRvl3ytVSw",
    true, //extraSecurity
    false //showUnavailableSJ
);

$peskeraStoreSettings = new StoreSettings(
    true,                          // useArrows
    true,                         // useDots
    false,                          // useThumbnail
    'peskera',                    // groupName
    1,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    ['pitch', 'mobileHomeGlamping'],       // accommodationTypes
    [],       // specialConditions
    false,                          // promoCode
    60000,                             // refreshAvailabilityPeriod
    [13.8512697,44.8224466],                             // initialRecenter
    "Peskera",                             // mapPath
    true,//occupancyOptimization 
    false,                           //show360onModal
    null,
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtNGwyeWY0bTByZGEyanNscTV2Y21mamIifQ.Vzqh3y_KNbWwubc-Laemog", 
    true, //extraSecurity
    true, //showUnavailableSJ
);

$floriaStoreSettings = new StoreSettings(
    true,                          // useArrows
    true,                         // useDots
    false,                          // useThumbnail
    'floria',                    // groupName
    1,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    ['pitch', 'mobileHomeGlamping'],       // accommodationTypes
    [],       // specialConditions
    false,                          // promoCode
    60000,                             // refreshAvailabilityPeriod
    [14.12756327381365,45.075387180883325,17.8],                             // initialRecenter
    "Floria",                             // mapPath
    false,//occupancyOptimization
    false,                           //show360onModal
    null,
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtNW1mMTlvaDFuY2cybXNnY3U1aHlmc3gifQ.AYQJy3_Gr9Gv_acUzXt_tQ",
    true, //extraSecurity
    true, //showUnavailableSJ
);

$turnirStoreSettings = new StoreSettings(
    true,                         // useArrows
    true,                          // useDots
    false,                         // useThumbnail
    'turnir',                     // groupName
    1,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    null,                // accommodationTypes
    [], // specialConditions 
    false,                         // promoCode
    45000,                             // refreshAvailabilityPeriod
    [13.840960991480529,45.10570917746665],                             // initialRecenter
    'Turnir',                             // mapPath
    false,//occupancyOptimization 
    true,                           //show360onModal
    "mapbox://styles/trimrd/cm1tes6nt018p01qpd70i4w4t",
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtMXRmZHhocjAxZ3EyaXNiamEyYmxwNWwifQ.kI3ldRD57x_xxRvl3ytVSw",
    true, //extraSecurity
    false //showUnavailableSJ
);

$avalonaStoreSettings = new StoreSettings(
    true,                         // useArrows
    true,                          // useDots
    false,                         // useThumbnail
    'aminess',                     // groupName
    15,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    null,                // accommodationTypes
    [], // specialConditions 
    false,                         // promoCode
    45000,                             // refreshAvailabilityPeriod
    [15.097012138996632,44.33662610080435],                             // initialRecenter
    'Avalona',                             // mapPath
    false,//occupancyOptimization 
    false,                           //show360onModal
    "mapbox://styles/trimrd/cm2szfr1n00di01pi87gcfw34",
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtMXRmZTl4djAxdzAyanF1YTd1d3J2Z20ifQ.xM3Mp7RzB4f5bYRvNSaWUw",
    false, //extraSecurity
    false, //showUnavailableSJ
    true, //EUColoring
    true, //showAttribution
    true //simplerFilter
);

$brijuniStoreSettings = new StoreSettings(
    true,                          // useArrows
    true,                         // useDots
    false,                          // useThumbnail
    'brijuni',                    // groupName
    1,                             // propertyId
    null,                          // oldMapParameters
    null,                          // oldMapSettings
    ['hotel', 'nautic'],       // accommodationTypes
    [],       // specialConditions
    false,                          // promoCode
    60000,                             // refreshAvailabilityPeriod
    [13.75796798442093,44.912079207864764],                             // initialRecenter
    "Brijuni",                             // mapPath
    false,//occupancyOptimization
    false,                           //show360onModal
    null,
    "pk.eyJ1IjoidHJpbXJkIiwiYSI6ImNtNW1mMTlvaDFuY2cybXNnY3U1aHlmc3gifQ.AYQJy3_Gr9Gv_acUzXt_tQ",
    true, //extraSecurity
    true, //showUnavailableSJ
    true, //EUColoring
    true, //showAttribution
);

// Combine Property and StoreSettings under a unified properties object
$properties = [
    'cikat' => $cikatStoreSettings,
    'polidor' => $polidorStoreSettings,
    'oikos' => $oikosStoreSettings,
    'buqezandvita' => $oikosBuqezStoreSettings,
    'tiha' => $tihaStoreSettings,
    'atea' => $ateaStoreSettings,
    'polari' => $polariStoreSettings,
    'valkanela' => $valkanelaStoreSettings,
    'vestar' => $vestarStoreSettings,
    'amarin' => $amarinStoreSettings,
    'portosole' => $portoSoleStoreSettings,
    'zaton' => $zatonStoreSettings,
    'slatina' => $slatinaStoreSettings,
    'bijar' => $bijarStoreSettings,
    'baldarin' => $baldarinStoreSettings,
    'sirena' => $sirenaStoreSettings,
    'maravea' => $maraveaStoreSettings,
    'maslinica' => $maslinicaStoreSettings,
    'oliva' => $olivaStoreSettings,
    'koversada' => $koversadaStoreSettings,
    'peskera' => $peskeraStoreSettings,
    'floria' => $floriaStoreSettings,
    'turnir' => $turnirStoreSettings,
    'avalona' => $avalonaStoreSettings,
    'brijuni' => $brijuniStoreSettings
];

// Check if a specific property is requested via the 'property' query parameter
$requestedProperty = isset($_GET['property']) ? $_GET['property'] : null;

// Return the JSON for the requested property, or all properties if none specified
if ($requestedProperty && isset($properties[$requestedProperty])) {
    // Return the requested property as JSON
    echo json_encode($properties[$requestedProperty], JSON_PRETTY_PRINT);
} else {
    // If no property specified, return all properties
    echo json_encode($properties, JSON_PRETTY_PRINT);
}
?>
