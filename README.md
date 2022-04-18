# IPcalcavel

## General
This is a tool for calculating IP subnets. 

## Installation
**method 1 : clone via GIT**
1. Open terminal/command prompt and enter the `public_html` directory of your webserver
2. Within the `public_html` directory run: `git clone git@github.com:mikenikon/IPcalcavel.git .`
3. Use your browser and go to the correct IP/domain of your webserver

**method 2 : transfer via FTP**
1. From Github download the .zip file of the repository via the green 'code' button (on the <>code page)
2. Unzip the file
3. Via FTP copy the *entire content* of the `IPcalcavel-main folder` into the `public_html` folder of your webserver
4. Use your browser and go to the correct IP/domain of your webserver

**method 3 : using Laravel (only if it is installed on your machine)**
1. Start a new Laravel project, for example `laravel new IPcalcavel`
2. From Github download the .zip file of the repository via the green 'code' button (on the <>code page)
3. Unzip the file
4. Overwrite the `IPcalcavel/resources/views/welcome.blade.php` file within the created Laravel project with the same file from the downloaded .zip file
5. run `php artisan serve` and go to `http://localhost:8000` in your browser

## Usage
**method 1 : via a webbrowser**
After opening the application from your favourite webbrowser:

1. Enter a valid IP subnet (IPv4 or IPv6) using CIDR notation, for example `192.168.1.2/24`
2. Choose if you want the output as HTML, JSON or XML
3. Press 'calculate'

**method 2 : asynchronous via Javascript**
Please make sure the Javascript is being run from within the same Laravel application, as the CSFR token needs to be the same.

1. determine the csrf token using PHP
2. build a correct URL to IPcalcavel with *_token*, *input* and *output* GET variables
3. *input* is a string with a valid CIDR address
4. *output* is a string with 3 possible values:
    1. empty string (output = HTML)
    2. json (output = JSON)
    3. xml (output = XML)
5. The returned values of IPcalcavel are:
    1. network (string)
    2. first (string)
    3. last (string)
    4. hosts (string)
    5. errormessages (array)

**example: calculate 192.168.1.2/24 and return as JSON**
```
<?php
// what is the CSRF token?
$csrf = csrf_token();
?>

<!-- javascript in a blade template-->
<script>
function readTextFile(file, callback) {
    var rawFile = new XMLHttpRequest();
    rawFile.overrideMimeType("application/json");
    rawFile.open("GET", file, true);
    rawFile.onreadystatechange = function() {
        if (rawFile.readyState === 4 && rawFile.status == "200") {
            callback(rawFile.responseText);
        }
    }
    rawFile.send(null);
}

//example output will be written to console
var ip  = '192.168.1.2/24';
var out = 'JSON';
var csrf= {{ $csrf }};
var path= 'full/path/to/IPcalcavel/';

readTextFile(path + "?_token=" + csrf + "&input=" + ip + "&output=" + out, function(text){
    var data = JSON.parse(text);
    console.log(data);
});
</script>
```
