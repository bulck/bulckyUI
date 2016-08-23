<?php

$GLOBALS['PLUGIN_irrigation']['TRACE_LEVEL'] = "debug";

$GLOBALS['PLUGIN_irrigation']['localtechnique'] = array(
    'nom' => "localtechnique",
    'ip' => '192.168.0.53',
    'irriActive' => "true",
    'pompe' => array(
        "nom" => "Surpresseur",
        "prise" => 2
    ),
    'engrais' => array (
        '0' => array (
            "nom" => "engrais1",
            "prise" => 3,
            "active" => "false"
        ),
        '1' => array (
            "nom" => "engrais2",
            "prise" => 4,
            "active" => "false"
        ),
        '2' => array (
            "nom" => "engrais3",
            "prise" => 5,
            "active" => "false"
        ),
        '3' => array (
            "nom" => "eau",
            "prise" => 6,
            "active" => "false"
        )
    ),
);

$GLOBALS['PLUGIN_irrigation']['plateforme'][0] = array(
    'nom' => "montmartre",
    'ip' => '192.168.0.50',
    "active" => "true",
    'limitDesamorcagePompe'  => "false",
    'tempsPerco' => '40',
    'tempsMaxRemp' => '600',
    "priseDansLT" => 7,
    'pompe' => array(
        "nom" => "pompe",
        "prise" => 1
    ),
    'zone' => array (
        '0' => array (
            "nom" => "EV_sud",
            "prise" => 2,
            "tempsOn" => 80,
            "tempsOff" => 200,
            "active" => "true",
            "coef" => 1.0
        ),
        '1' => array (
            "nom" => "EV_milieu",
            "prise" => 3,
            "tempsOn" => 80,
            "tempsOff" => 200,
            "active" => "true",
            "coef" => 1.0
        ),
        '2' => array (
            "nom" => "EV_nord",
            "prise" => 4,
            "tempsOn" => 80,
            "tempsOff" => 200,
            "active" => "true",
            "coef" => 1.0
        )
    )
);

$GLOBALS['PLUGIN_irrigation']['plateforme'][1] = array(
    'nom' => "dantin",
    'ip' => '192.168.0.54',
    "active" => "true",
    'limitDesamorcagePompe'  => "false",
    'tempsPerco' => '10',
    'tempsMaxRemp' => '300',
    "priseDansLT" => 8,
    'pompe' => array(
        "nom" => "pompe",
        "prise" => 1
    ),
    'zone' => array (
        '0' => array (
            "nom" => "EV_nord",
            "prise" => 2,
            "tempsOn" => 30,
            "tempsOff" => 400,
            "active" => "true",
            "coef" => 1.0
        ),
        '1' => array (
            "nom" => "EV_sud",
            "prise" => 3,
            "tempsOn" => 30,
            "tempsOff" => 400,
            "active" => "true",
            "coef" => 1.0
        )
    )
);

$GLOBALS['PLUGIN_irrigation']['plateforme'][2] = array(
    'nom' => "centrale",
    'ip' => '192.168.0.55',
    "active" => "true",
    'limitDesamorcagePompe'  => "false",
    'tempsPerco' => '10',
    'tempsMaxRemp' => '300',
    "priseDansLT" => 9,
    'pompe' => array(
        "nom" => "pompe",
        "prise" => 1
    ),
    'zone' => array (
        '0' => array (
            "nom" => "EV",
            "prise" => 2,
            "tempsOn" => 30,
            "tempsOff" => 250,
            "active" => "true",
            "coef" => 1.0
        )
    )
);

$GLOBALS['PLUGIN_irrigation']['plateforme'][3] = array(
    'nom' => "mogador",
    'ip' => '192.168.0.51',
    "active" => "true",
    'limitDesamorcagePompe'  => "false",
    'tempsPerco' => '10',
    'tempsMaxRemp' => '600',
    "priseDansLT" => 10,
    'pompe' => array(
        "nom" => "pompe",
        "prise" => 1
    ),
    'zone' => array (
        '0' => array (
            "nom" => "EV_nord",
            "prise" => 2,
            "tempsOn" => 35,
            "tempsOff" => 250,
            "active" => "true",
            "coef" => 1.0
        ),
        '1' => array (
            "nom" => "EV_sud",
            "prise" => 3,
            "tempsOn" => 35,
            "tempsOff" => 250,
            "active" => "true",
            "coef" => 1.0
        )
    )
);

$GLOBALS['PLUGIN_irrigation']['plateforme'][4] = array(
    'nom' => "opera",
    'ip' => '192.168.0.52',
    "active" => "true",
    'limitDesamorcagePompe'  => "false",
    'tempsPerco' => '300',
    'tempsMaxRemp' => '600',
    "priseDansLT" => 11,
    'pompe' => array(
        "nom" => "pompe",
        "prise" => 1
    ),
    'zone' => array (
        '0' => array (
            "nom" => "EV_1",
            "prise" => 2,
            "tempsOn" => 150,
            "tempsOff" => 250,
            "active" => "true",
            "coef" => 1.0
        )
    )
);

?>
