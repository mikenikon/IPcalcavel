# IPcalcavel

## General
This is a tool for calculating IP subnets. 

## Installation
Laravel must be installed on your machine.

**method 1 : clone the entire project from git**
1. create an empty directory you want the project installed in and enter it using terminal
2. clone the entire project using:

`git clone git@github.com:mikenikon/IPcalcavel.git .`

3. from within the directory the project is installed in run:

`composer install`

4. copy the hidden `.env.example` file to `.env`:

*windows* 

`copy .env.example .env`

*osx/linux* 

`cp .env.example .env`

5. generate a key; run:

`php artisan key:generate`

6. start serving; run:

`php artisan serve`

7. In your browser, go to [http://localhost:8000](http://localhost:8000)

**method 2 : start a new Laravel project, and overwrite `/resources/views/welcome.blade.php`**
1. Start a new Laravel project, for example `laravel new IPcalcavel`
2. From Github download the .zip file of the repository via the green 'code' button (on the <>code page)
3. Unzip the file
4. Overwrite the `/resources/views/welcome.blade.php` file within the created Laravel project with the same file from the downloaded .zip file
5. run `php artisan serve` and go to [http://localhost:8000](http://localhost:8000) in your browser


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
