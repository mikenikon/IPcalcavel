<?php
/* class for calculations (PHP) starts here */


/* processing of the $_GET variables starts here */
$input = '';        // string

if($_GET) {
    $input = trim($_GET['input']);
}


/* initiate the $error_message array for storing error messages */
$error_message = array();


/* initiate the $network, $first, $last and $hosts variables for calculation results */
$network    = '';   // string
$first      = '';   // string
$last       = '';   // string
$hosts      = 0;    // int


/* if the user entered a subnet (IP), validate it first */
if($input) {
    // can you find a slash / ? If not, the subnet mask in prefix format is invalid
    if(!stristr($input, "/")) {
        $error_message[] = "Please enter a valid subnet mask in prefix format after the IP address using a slash (\"/\"), for example \"192.168.1.0/24\"";
    }

    // if you do have a slash, everything before the slash is "
    else {

    }

    // determine if we have a valid IPv4 address


    // determine if we have a valid IPv6 address



}


/* Do we have user input, and there are no errors? Then proceed with the calculations */
if(count($error_message) == 0 AND $input) {
}

// end of PHP code
?>

<!-- HTML output to browser starts here -->
<html>
    <head>
        <title>Subnet (IP) Calculator</title>
    </head>

    <body>

        <form method="get" action="">
            @csrf
            <div style='display: inline-block;'>
                Subnet (IP) : <input type="text" name="input"><br>
            </div>
            <div style='display: inline-block;'>
                <button type="submit">Calculate</button>
            </div>
        </form>

        <!-- if there are any errors, display them in red -->
        @if (count($error_message) > 0)
            <div style="color:red;">
                <ul>
                    @foreach ($error_message as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        <!-- no errors? then display the results in green -->
        @else
            <div>
                <table style="color:green;">
                    @if ($network)
                        <tr><td>network:</td><td> {{ $network }}</td></tr>
                    @endif
                    @if ($first)
                        <tr><td>first:</td><td> {{ $first }}</td></tr>
                    @endif
                    @if ($last)
                        <tr><td>last:</td><td> {{ $last }}</td></tr>
                    @endif
                    @if ($hosts)
                        <tr><td>hosts:</td><td> {{ $hosts }}</td></tr>
                    @endif
                </table>
            </div>
        @endif

    </body>
</html>
