<?php
/* show or hide debug info */
$debug_mode = TRUE; // TRUE = show , FALSE = hide

/* processing of the $_GET variables starts here */
$input  = ''; // assume there is no input

if($_GET) {
    $input = trim($_GET['input']);
}

/* class for calculations (PHP) starts here */
class IPcalc
{
    // output
    public string $network;             // outputs the network
    public string $first;               // outputs the first IP of the range
    public string $last;                // outputs the last IP of the range
    public int $hosts;                  // outputs the number of hosts

    public array $error_messages;       // array for error messages output
    public array $debug_info;           // array with debug info output

    public string $ip_type;             // are we dealing with IPv4 or IPv6?
    public string $base_ip;             // what is the IP address we are calculating with?
    public string $bitmask;             // what is the bitmask the user entered?

    public function __construct(string $input)
    {
        // initialize
        $this->network              = '';
        $this->first                = '';
        $this->last                 = '';
        $this->hosts                = 0;

        $this->error_messages        = array();
        $this->debug_info           = array();

        $this->ip_type              = '';
        $this->base_ip              = '';
        $this->bitmask   = '';

        // do we have valid input?
        if($this->Validate($input)) {
            // yes, the input is valid let's do the calculations
            if($this->ip_type == 'IPv4') {
                $this->IPv4Calc();
            }
            elseif($this->ip_type == 'IPv6') {
                $this->IPv6Calc();
            }
        }
    }

    private function Validate(string $input) :bool
    {
        // can you find a slash / ? If not, the subnet mask in prefix format is invalid
        if(!stristr($input, "/")) {
            $this->error_messages[] = 'Please enter a valid bitmask
            after the IP address using a forward slash ("/"), for example
            "192.168.1.0/24"';
        }

         // split into $base_ip (before /) and $bitmask (after /)
        $this->base_ip = trim(stristr($input, '/', true));

        $this->bitmask = stristr($input, '/');
        $this->bitmask = ltrim($this->bitmask, '/');


        // debug info
        $this->debug_info[] = 'base_ip: ' . $this->base_ip;
        $this->debug_info[] = 'bitmask: ' . $this->bitmask;

        // determine if we have a valid IPv4 address
        if (filter_var($this->base_ip, FILTER_VALIDATE_IP)) {
            $this->ip_type = 'IPv4';
        }

        // determine if we have a valid IPv6 address
        if (filter_var($this->base_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $this->ip_type = 'IPv6';
        }

        // debug info
        $this->debug_info[]                = 'ip_type: ' . $this->ip_type;

        if($this->ip_type != 'IPv4' AND $this->ip_type != 'IPv6') {
            $this->error_messages[] = $input . ' does not contain a valid IPv4 or IPv6
            address';
        }

        // check if the subnet mask in prefix format is valid (IPv4)
        if($this->ip_type == 'IPv4') {
            if($this->bitmask < 1 OR $this->bitmask > 32
            OR !is_numeric($this->bitmask)) {
                $this->error_messages[] = $this->bitmask . ' is an invalid
                bitmask for IPv4, please choose a value in between 1 and 32';
            }
        }

        // check if the subnet mask in prefix format is valid (IPv4)
        if($this->ip_type == 'IPv6') {
            if($this->bitmask < 1 OR $this->bitmask > 128
            OR !is_numeric($this->bitmask)) {
                $this->error_messages[] = $this->bitmask . ' is an invalid
                bitmask for IPv6, please choose a value in between 1 and 128';
            }
        }

        // return 0 = did not validate or 1 = did validate
        if($this->error_messages) {
            return 0; // input is not valid
        }
        else {
            return 1; // input is valid
        }
    }


    private function IPv4Calc() :void
    {
        // split the entered base IP into 4 blocks
        $block      = array(); // normal
        $block_bin  = array(); // binary

        list($block[1], $block[2], $block[3], $block[4]) = explode(".", $this->base_ip);

        // convert the base IP to binary
        for($x = 1; $x < 5; $x++) {
            $block_bin[$x] = sprintf( '%08d', decbin( $block[$x] ));
        }

        $base_ip_bin =  $block_bin[1] .
                        $block_bin[2] .
                        $block_bin[3] .
                        $block_bin[4];

        $this->debug_info[] = 'base_ip BIN: ' . $base_ip_bin;


        // Calculate network
        $network_bin    = substr($base_ip_bin, 0, $this->bitmask);

        // add 0 until bit 32
        $y = 32 - $this->bitmask;

        for($x = 0; $x < $y; $x++) {
            $network_bin .= '0';
        }
        $this->debug_info[] = "network BIN: " . $network_bin;


        // Calculate first
        $first_bin      = substr($base_ip_bin, 0, $this->bitmask);

        // add 0 until bit 31
        $y = 31 - $this->bitmask;

        for($x = 0; $x < $y; $x++) {
            $first_bin .= '0';
        }
        // add final 1
        if($this->bitmask < 32) {
            $first_bin .= '1';
        }
        else {
            $first_bin = substr($base_ip_bin, 0, 31) . '1';
        }

        $this->debug_info[] = "first BIN: " . $first_bin;

        // Calculate last
        $last_bin      = substr($base_ip_bin, 0, $this->bitmask);

        // add 1 until bit 31
        $y = 31 - $this->bitmask;

        for($x = 0; $x < $y; $x++) {
            $last_bin .= '1';
        }
        // add final 0
        if($this->bitmask < 32) {
            $last_bin .= '0';
        }
        else {
            $last_bin = substr($base_ip_bin, 0, 31) . '0';
        }

        $this->debug_info[] = "last BIN: " . $last_bin;

        // Calculate number of hosts
        $number_of_hosts = pow(2, (32 - $this->bitmask)) - 2;

        if($number_of_hosts < 1) {
            $number_of_hosts = 1;
        }

        // output
        $this->network  = $this->BINtoIPv4($network_bin) . "/" . $this->bitmask;
        $this->first    = $this->BINtoIPv4($first_bin);
        $this->last     = $this->BINtoIPv4($last_bin);
        $this->hosts    = $number_of_hosts;
    }

    private function BINtoIPv4($binary) :string
    {
        $string = '';

        $string .= bindec(substr($binary, 0, 8)) . ".";
        $string .= bindec(substr($binary, 8, 8)) . ".";
        $string .= bindec(substr($binary, 16, 8)) . ".";
        $string .= bindec(substr($binary, 24, 8));

        return $string;
    }
}

// create the $IPcalc object
$IPcalc = new IPcalc($input);


// end of PHP code
?>

<!-- HTML output to browser starts here -->
<html>
    <head>
        <title>Subnet (IP) Calculator</title>
    </head>

    <body>
        <h1>Subnet (IP) Calculator</h1>

        <form method='get' action=''>
            @csrf
            <div style='display: inline-block;'>
                Subnet (IP) : <input type='text' name='input'><br>
            </div>
            <div style='display: inline-block;'>
                <button type='submit'>Calculate</button>
            </div>
        </form>
        @if($input)
            <!-- if there are any errors, display them in red -->
            @if (count($IPcalc->error_messages) > 0)
                <div style='color:red;'>
                    <ul>
                        @foreach ($IPcalc->error_messages as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            <!-- no errors? then display the results in green -->
            @else
                <div>
                    <table style='color:green;'>
                        @if ($IPcalc->network)
                            <tr><td>network:</td><td> {{ $IPcalc->network }}</td></tr>
                        @endif
                        @if ($IPcalc->first)
                            <tr><td>first:</td><td> {{ $IPcalc->first }}</td></tr>
                        @endif
                        @if ($IPcalc->last)
                            <tr><td>last:</td><td> {{ $IPcalc->last }}</td></tr>
                        @endif
                        @if ($IPcalc->hosts)
                            <tr><td>hosts:</td><td> {{ $IPcalc->hosts }}</td></tr>
                        @endif
                    </table>
                </div>
            @endif
            <!-- if there is any debugging info, display it in orange -->
            @if (count($IPcalc->debug_info) > 0 AND $debug_mode == 1)
                <div style='color:orange;'>
                    <ul>
                        @foreach ($IPcalc->debug_info as $info)
                            <li>{{ $info }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endif

    </body>
</html>
