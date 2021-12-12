<?php

//--------------------------------------

$csv = new Csv();
$csv->seperator = ";";
$csv->csvFileName = "ausgabe.csv";
$csv->newLine = "\r\n";

//--------------------------------------

//Version
if (!isset($_POST["formid"])) {
    echo "<center><h1 style=\"font-family: monospace;\">DA-FormMaker CSV Script</h1></center>";
    echo "<center><p style=\"font-family: monospace;\">Version 1.0.0</p></center>";
    echo "<center><p style=\"font-family: monospace;\">Info: <a href=\"https://www.da-software.de\">da-software.de</a></p></center>";
    die();
}

const BASE64START = "data:image/png;base64,";

foreach ($_POST as $name => $value) {
    $str = stripslashes($value);
    //$str = str_replace("\n", "<br>", $str);

    if (checkTransmitField($name) == true) {
        if (!(isset($_POST["hide_empty_fields"])) || (!($_POST["hide_empty_fields"]) == "-1") || ($str != "")) {
            if (!isset($_POST["check_conf__" . $name]) || (isset($_POST["check__conf__" . $name]) && isset($_POST["check__" . $name]))) { //Überprüfung ob Checkbox-Feld (z.B. Eingabegeld welches aktivierbar ist)
                if (isset($_POST["label__" . $name])) {
                    $shownName = base64_decode($_POST["label__" . $name]);
                } else {
                    $shownName = $name;
                }

                if (startsWith($name, "Intermediate_")) {
                    //ignore intermediate texts
                } else {
                    if (startsWith($str, BASE64START)) {
                        //ignore signature
                    } else {
                        $csv->add($str);

                    }
                }
            }
        }
    }
}

$csv->storeLine();

//Redirect to thank you page
$redir   = $_POST["redirect"];
header("Location:" . $redir);

//----------------------------------------------------------------------------------------------------------------------

//Classes

/**
 * CSV class
 */
class Csv {
    /**
     * the file name to save to
     * @var string
     */
    public $csvFileName = "output.csv";

    /**
     * the seperator that should be used
     * @var string
     */
    public $seperator = ";";

    /**
     * the newline character
     * @var string
     */
    public $newLine = "\n";

    /**
     * the escape char for items
     * @var string
     */
    public $escape = "\"";

    /**
     * the current line
     * @var string
     */
    private $line = "";

    /**
     * Add an item to the current csv line
     * @param $str
     * @return void
     */
    public function add($str) {
        $s = $this->prepareString($str);
        $this->line .= $s . $this->seperator;
    }

    /**
     * store the current line, saves to file and closes
     * the file
     * @return void
     */
    public function storeLine() {
        $this->line .= $this->newLine;
        file_put_contents($this->csvFileName,  $this->line , FILE_APPEND | LOCK_EX);
        $this->line = "";
    }

    /**
     * Prepares a string for storing
     * @param $s
     * @return string
     */
    private function prepareString($s): string
    {
        if (contains($this->escape, $s)) {
            $s = str_replace($this->escape, $this->escape . $this->escape, $s);
        }

        if (contains(" ", $s)) {
            $s = $this->escape . $s . $this->escape;
        }

        return $s;
    }
}

//Functions

/**
 * Checks if a string starts with a certain string
 * @param string $haystack
 * @param string $needle
 * @return boolean
 */
function startsWith(string $haystack, string $needle): bool
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}


/**
 * @param $needle
 * @param $haystack
 * @return bool
 */
function contains($needle, $haystack): bool
{
    return strpos($haystack, $needle) !== false;
}


/**
 * @param String $name
 * @return boolean
 */
function checkTransmitField(string $name): bool
{
    $res = true;

    if ($name == "redirect") $res = false;
    if ($name == "einleittext") $res = false;
    if ($name == "probe") $res = false;
    if ($name == "crypt") $res = false;
    if ($name == "ReturnToSender") $res = false;
    if ($name == "next") $res = false;
    if ($name == "typemail") $res = false;
    if ($name == "admin") $res = false;
    if ($name == "admin1") $res = false;
    if ($name == "admin2") $res = false;
    if ($name == "subject") $res = false;
    if ($name == "captcha_input") $res = false;
    if ($name == "formid") $res = false;
    if ($name == "settings") $res = false;
    if ($name == "copyfields") $res = false;
    if ($name == "copyip") $res = false;
    if ($name == "hide_empty_fields") $res = false;
    if ($name == "settings_encoding") $res = false;
    if ($name == "settings_error_iplock") $res = false;
    if ($name == "settings_error_fileerror") $res = false;
    if ($name == "settings_error_captcha") $res = false;
    if ($name == "PHPSESSID") $res = false;
    if ($name == "dropMonthSelect") $res = false;
    if ($name == "dropYearSelect") $res = false;
    if ($name == "internal_value") $res = false;
    if (substr($name, 0, 8) == "anzeige_") $res = false;
    if (startsWith($name, "label__")) $res = false;
    if (startsWith($name, "conf_")) $res = false;
    if (startsWith($name, "check_")) $res = false;

    return $res;
}